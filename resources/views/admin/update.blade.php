@extends("admin.layouts.app")

@section("title", "Update Product")
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
                        <img src="{{ asset('assets/' . $skus->image) }}"alt="{{ $skus->sku_code }}"
                        class="img-fluid" id="preview-image" style="max-width: 100%; max-height: 300px; object-fit: contain;">
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
                            <h2 class="text-primary text-center mb-3">Update Product</h2>
                            <p class="text-muted text-center">Modify the product details in the inventory system.</p><hr>

                            <!-- Form -->
                            <form action="{{ route('sku.update.submit', $skus->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="image" class="form-label fw-bold">Product Image:</label>
                                    <input type="file" class="form-control" id="image" name="image">
                                </div>

                                <div class="mb-3">
                                    <label for="sku_code" class="form-label fw-bold">Product SKU:</label>
                                    <input type="text" class="form-control text-uppercase" id="sku_code" name="sku_code" value="{{ $skus->sku_code }}" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="zone_id" class="form-label fw-bold">Select Zone :</label>
                                            <select class="form-select" id="zone_id" name="zone_id" required>
                                                <option disabled>Select a zone</option>
                                                @foreach($zones as $zone)
                                                    <option value="{{ $zone->id }}" {{ $skus->zone_id == $zone->id ? 'selected' : '' }}>
                                                        {{ strtoupper($zone->zone_name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="rack_id" class="form-label fw-bold">Select Rack :</label>
                                            <select class="form-select" id="rack_id" name="rack_id" data-selected="{{ $skus->rack_id }}">
                                                <option disabled>Select a rack</option>
                                                @foreach($storacks as $storack)
                                                    <option value="{{ $storack->rack->id }}" {{ $skus->rack_id == $storack->rack->id ? 'selected' : '' }}>
                                                        {{ strtoupper($storack->rack->rack_number) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> --}}

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="rack_id" class="form-label fw-bold">Select Rack :</label>
                                            <select class="form-select" id="rack_id" name="rack_id" data-selected="{{ $skus->rack_id ?? '' }}">
                                                <option disabled>Select a rack</option>
                                                @foreach($storacks as $storack)
                                                    <option value="{{ $storack->rack->id }}" {{ $skus->rack_id == $storack->rack->id ? 'selected' : '' }}>
                                                        {{ strtoupper($storack->rack->rack_number) }}
                                                    </option>
                                                @endforeach
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
        // 将 storacks 数据转换为 storacksData，确保 rack 存在
        window.storacksData = @json($storacks->map(function($storack) {
            return [
                'id' => $storack->rack->id ?? null,
                'rack_number' => $storack->rack->rack_number ?? '',
                'zone_id' => $storack->zone_id
            ];
        }));
    </script>

    <script src="{{ asset('assets/js/edit.js') }}"></script>
@endsection
