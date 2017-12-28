@extends('home.layout')

@section('title')
    {{ trans('home.edit_gallery') }}
@endsection

@section('pageTitle')
    {{ trans('home.edit_gallery') }}
@endsection

@section('pageDescription')
    {{ trans('home.edit_gallery_page_description') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-lg-10">
            @include('home.posts.partials.formMessages')
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('home.create_your_gallery') }}</h3>
                </div>
                <?php $url = 'home/gallery/store'; ?>
                @if (!empty($gallery))
                    <?php $url = 'home/gallery/update/' . $gallery->id; ?>
                @endif
                {!! Form::open(['url' => $url, 'class' => 'form', 'files' => true]) !!}
                <div class="box-body">
                    <div class="form-group">
                        <label for="title">{{ trans('home.title') }}</label>
                        <input type="text" class="form-control" id="title" name="title"
                               placeholder="{{ trans('home.gallery_title') }}"
                               value="{{ !empty($gallery) ? $gallery->title : '' }}">
                    </div>
                    @if (!empty($gallery))
                        <div class="row top15">
                            <div class="col-lg-12">
                                <span><strong>Shortcode</strong></span>
                            </div>
                            <div class="col-lg-12">
                                Insert this code where you want to show this gallery in your post:
                                [gallery id='{{ $gallery->id }}']
                            </div>
                        </div>
                    @endif
                    <div class="row top15" id="gallery-images-container">
                        @if (!empty($images))
                            @foreach($images as $image)
                                <div class="gallery-img col-sm-6 col-md-4 col-lg-3">
                                    <span data-image-id="{{ $image->id }}" class="close js-delete-image">×</span>
                                    <img class="img-responsive"
                                         src="{{ \App\Http\Controllers\ImageController::getPublicImageUrl($image->image, true) }}">
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="box-footer">
                        <span class="btn btn-primary btn-file">
                            Subir imágenes
                            <input id="image-file-input" class="js-file-input" data-image="image" name="images[]"
                                   type="file" multiple>
                        </span>
                    {!! Form::submit(trans('home.save'),['class' => 'btn btn-primary pull-right']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('custom-javascript')
    {!! Html::script('admin/assets/js/galleries.js') !!}
@endsection
