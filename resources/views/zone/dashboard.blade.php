@extends("admin.layouts.app")

@section("title", "Admin Panel")
@section("content")

<div class="container text-center mt-5">
    <!-- Success Alert -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Error Alert -->
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center">
        <h3 class="text-md-start mb-0">Zone List Management</h3>

        <form class="d-flex gap-3 align-items-center" role="search" id="search-form">
            <select class="form-select" id="zone_id" name="zone_id" style="width: 150px;">
                <option selected value="">Select a Zone</option>
                @foreach($zones as $zone)
                    <option value="{{ $zone->id }}">{{ strtoupper($zone->zone_name) }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <hr>

    <!-- 卡片列表 -->
    <div id="zone-card-container" class="row row-cols-1 row-cols-md-3 g-4"></div>

    <div id="no-results" class="text-center py-3" style="display: none;">No results found.</div>

    <!-- Pagination -->
    <nav aria-label="Page navigation example" class="d-flex justify-content-center mt-3">
        <ul id="pagination" class="pagination"></ul>
    </nav>
</div>

<script>
    $(document).ready(function() {
        let currentZone = '';

        // 初始加载
        loadZones(1);

        // 监听搜索框变化
        $('#zone_id').on('change', function() {
            currentZone = $(this).val();
            loadZones(1);
        });

        function loadZones(page) {
            $.ajax({
                url: "{{ route('zone.list') }}",
                type: 'GET',
                data: {
                    page: page,
                    zone_id: currentZone,
                    length: 9  // 每页显示 9 个
                },
                success: function(response) {
                    renderCards(response.data);
                    renderPagination(response.current_page, response.last_page);
                    $('#no-results').toggle(response.data.length === 0);
                },
                error: function(xhr) {
                    console.error('Error loading zones:', xhr);
                }
            });
        }

        function renderCards(data) {
            let container = $('#zone-card-container');
            container.empty();

            data.forEach(zone => {
                container.append(`
                    <div class="col">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <img src="{{ asset('assets/icons/zone.png') }}"
                                alt="Zone Icon" class="card-img-top" style="width: 50px; height: 50px; object-fit: contain;">
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title">${zone.zone_name}</h5>
                            </div>
                            <div class="card-footer text-body-secondary">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('zone.update', '') }}/${zone.id}" class="btn btn-warning btn-sm w-50">Edit</a>
                                    <form action="{{ route('zone.destroy', '') }}/${zone.id}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Zone?');" class="w-50">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-danger btn-sm w-100">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            });
        }

        function renderPagination(currentPage, lastPage) {
            let pagination = $('#pagination');
            pagination.empty();

            // Previous Button
            if (currentPage > 1) {
                pagination.append(`<li class="page-item">
                    <a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>`);
            }

            // Page Numbers
            for (let i = Math.max(1, currentPage - 2); i <= Math.min(lastPage, currentPage + 2); i++) {
                pagination.append(`<li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`);
            }

            // Next Button
            if (currentPage < lastPage) {
                pagination.append(`<li class="page-item">
                    <a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>`);
            }

            // 绑定事件，防止重复绑定
            $('.page-link').off('click').on('click', function (e) {
                e.preventDefault();
                const page = $(this).data('page');
                loadZones(page);
            });
        }
    });
</script>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
@endsection
