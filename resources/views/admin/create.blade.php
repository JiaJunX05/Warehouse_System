@extends("admin.layouts.app")

@section("title", "Create Product")
@section("content")

<!-- JQuery CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0">
                <div class="row g-0">

                    <div class="col-md-5 d-flex align-items-center justify-content-center p-3 bg-light">
                        <img src="{{ asset('assets/icons/Logo.png') }}"
                            alt="Preview" class="img-fluid" id="preview-image" style="max-width: 100%; max-height: 300px; object-fit: contain;">
                    </div>

                    <div class="col-md-7">
                        <div class="card-body p-4">

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

                            <!-- Form Title -->
                            <h2 class="text-primary text-center mb-3">Create Product</h2>
                            <p class="text-muted text-center">Add a new product to the inventory system.</p><hr>

                            <!-- Form -->
                            <form action="{{ route('sku.create.submit') }}" method="post" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label for="image" class="form-label fw-bold">Product Image:</label>
                                    <input type="file" class="form-control" id="image" name="image" required>
                                </div>

                                <div class="mb-3">
                                    <label for="sku_code" class="form-label fw-bold">Product SKU:</label>
                                    <input type="text" class="form-control text-uppercase" id="sku_code" name="sku_code" placeholder="Enter Product SKU" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="zone_id" class="form-label fw-bold">Select Zone :</label>
                                            <select class="form-select" id="zone_id" name="zone_id" required>
                                                <option selected disabled value="">Select a zone</option>
                                                @foreach($zones as $zone)
                                                    <option value="{{ $zone->id }}">{{ strtoupper($zone->zone_name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="rack_id" class="form-label fw-bold">Select Rack :</label>
                                            <select class="form-select" id="rack_id" name="rack_id" disabled>
                                                <option selected disabled value="">Select a rack</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 shadow-sm mt-3">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        // 将 storacksData 定义在全局作用域
        window.storacksData = @json($storacks);
    </script>

    <script src="{{ asset('assets/js/create.js') }}"></script>
@endsection
