@extends("layouts.app")

@section("title", "User Panel")
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
        <table class="table table-hover table-striped" id="sku-table">
            <thead class="table-dark" style="position: sticky; top: 0; z-index: 1;">
                <tr>
                    <th scope="col" class="fw-bold">Product Image</th>
                    <th scope="col" class="fw-bold">Product SKU</th>
                    <th scope="col" class="fw-bold">Zone Name</th>
                    <th scope="col" class="fw-bold">Rack Number</th>
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
                url: "{{ route('dashboard') }}",
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
                    </tr>
                `);
            });
        }

        function renderPagination(currentPage, lastPage) {
            let pagination = $('#pagination');
            pagination.empty();

            // 上一页
            if (currentPage > 1) {
                pagination.append(`
                    <li class="page-item">
                        <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
                    </li>
                `);
            }

            // 页码
            for (let i = Math.max(1, currentPage - 2); i <= Math.min(lastPage, currentPage + 2); i++) {
                pagination.append(`
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `);
            }

            // 下一页
            if (currentPage < lastPage) {
                pagination.append(`
                    <li class="page-item">
                        <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
                    </li>
                `);
            }

            // 分页点击事件
            $('.page-link').on('click', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                loadTable(page);
            });
        }
    });
</script>
@endsection
