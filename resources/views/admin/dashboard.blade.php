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

    <h3 class="text-md-start">SKU List Management</h3><hr>

    <div class="mb-3">
        <div class="text-md-end">
            <div class="card shadow-sm">
                <div class="card-body p-3">
                    <form class="d-flex gap-3 align-items-center" role="search" id="search-form">
                        <div class="input-group flex-grow-1">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                            <input class="form-control border-start-0" type="search" placeholder="Search by SKU..." aria-label="Search" id="search-input">
                            <button class="btn btn-outline-primary" type="submit" style="width: 150px;">Search</button>
                        </div>

                        <select class="form-select" id="zone_id" name="zone_id" style="width: 150px;">
                            <option selected value="">Select a Zone</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}">{{ strtoupper($zone->zone_name) }}</option>
                            @endforeach
                        </select>

                        <select class="form-select" id="rack_id" name="rack_id" style="width: 150px;">
                            <option selected value="">Select a Rack</option>
                            @foreach($racks as $rack)
                                <option value="{{ $rack->id }}">{{ strtoupper($rack->rack_number) }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive shadow-sm rounded-3">
        <table class="table table-hover table-striped">
            <thead class="table-dark" style="position: sticky; top: 0; z-index: 1;">
                <tr>
                    <th scope="col" class="fw-bold col-2">Product Image</th>
                    <th scope="col" class="fw-bold col-3">Product SKU</th>
                    <th scope="col" class="fw-bold col-2">Zone Name</th>
                    <th scope="col" class="fw-bold col-2">Rack Number</th>
                    <th scope="col" colspan="2" class="fw-bold col-3">Action</th>
                </tr>
            </thead>
            <tbody id="table-body"></tbody>
        </table>

        <div id="no-results" class="text-center py-3" style="display: none;">No results found.</div>
    </div>

    <!-- Pagination -->
    <nav aria-label="Page navigation example" class="d-flex justify-content-center mt-3">
        <ul id="pagination" class="pagination"></ul>
    </nav>
</div>

<script>
    $(document).ready(function() {
        let currentSearch = '';
        let currentZone = '';
        let currentRack = '';

        // 初始加载表格
        loadTable(1);

        // 搜索表单提交事件
        $('#search-form').on('submit', function(e) {
            e.preventDefault();
        });


        // 表单提交事件
        $('#search-form').on('input', function(e) {
            e.preventDefault();
            currentSearch = $('#search-input').val();
            currentZone = $('#zone_id').val();
            currentRack = $('#rack_id').val();
            loadTable(1);
        });

        // 选择框变化时触发搜索
        $('#zone_id, #rack_id').on('change', function() {
            currentSearch = $('#search-input').val();
            currentZone = $('#zone_id').val();
            currentRack = $('#rack_id').val();
            loadTable(1);
        });

        function loadTable(page) {
            $.ajax({
                url: "{{ route('admin.dashboard') }}",
                type: 'GET',
                data: {
                    page: page,
                    search: currentSearch,
                    zone_id: currentZone,
                    rack_id: currentRack,
                    length: 10
                },
                success: function(response) {
                    renderTable(response.data);
                    renderPagination(response.current_page, response.last_page);

                    // 显示/隐藏无结果提示
                    $('#no-results').toggle(response.data.length === 0);
                },
                error: function(xhr) {
                    console.error('Error loading table:', xhr);
                }
            });
        }

        function renderTable(data) {
            let tableBody = $('#table-body');
            tableBody.empty();

            data.forEach(sku => {
                tableBody.append(`
                    <tr>
                        <td><img src="{{ asset('assets') }}/${sku.image || 'default.jpg'}" alt="${sku.sku_code}" class="img-fluid" style="max-width: 50px;"></td>
                        <td>${sku.sku_code}</td>
                        <td>Zone: ${sku.zone ? sku.zone.zone_name.toUpperCase() : 'N/A'}</td>
                        <td>Rack: ${sku.rack ? sku.rack.rack_number.toUpperCase() : 'N/A'}</td>
                        <td><a href="{{ route('sku.update', '') }}/${sku.id}" class="btn btn-warning btn-sm" style="width: 100px;">Edit</a></td>
                        <td>
                            <form action="{{ route('sku.destroy', '') }}/${sku.id}" method="POST" onsubmit="return confirm('Are you sure you want to delete this SKU?');" class="w-100">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger btn-sm" style="width: 100px;">Delete</button>
                            </form>
                        </td>
                    </tr>
                `);
            });
        }

        function renderPagination(currentPage, lastPage) {
            let pagination = $('#pagination');
            pagination.empty();

            let paginationHtml = `
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                            <a class="page-link" href="#" aria-label="Previous" data-page="${currentPage - 1}">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>`;

            for (let i = 1; i <= lastPage; i++) {
                paginationHtml += `
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>`;
            }

            paginationHtml += `
                        <li class="page-item ${currentPage === lastPage ? 'disabled' : ''}">
                            <a class="page-link" href="#" aria-label="Next" data-page="${currentPage + 1}">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>`;

            pagination.html(paginationHtml);

            // 绑定点击事件
            $('.page-link').on('click', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page >= 1 && page <= lastPage) {
                    loadTable(page);
                }
            });
        }
    });
</script>
@endsection
