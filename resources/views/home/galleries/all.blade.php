@extends('home.layout')

@section('title')
    {{ trans('home.galleries') }}
@endsection

@section('pageTitle')
    {{ trans('home.galleries') }}
@endsection

@section('pageDescription')
    {{ trans('home.galleries_page_description') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12 col-md-6 col-lg-6">
            <div class="box">
                <div class="box-body">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered table-striped dataTable" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th class="sorting_asc" style="width: 20%;">
                                            Título
                                        </th>
                                        <th class="sorting hidden-xs" style="width: 15%;">
                                            Autor
                                        </th>
                                        <th class="sorting" style="width: 10%;">
                                            Acciones
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if (count($galleries) > 0)
                                        @foreach($galleries as $gallery)
                                            <tr role="row" class="odd">
                                                <td>{{ $gallery->title }}</td>
                                                <td class="hidden-xs">
                                                    <a href="http://geekytheory.com/home/galleries/{{ $gallery->user_username }}">
                                                        {{ $gallery->user_name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="{{ url('home/gallery/edit/' . $gallery->id) }}"
                                                       class="margin">
                                                    <span class="label bg-blue">
                                                         {{ trans('home.edit') }}
                                                    </span>
                                                    </a>
                                                    <a href="{{ url('home/gallery/delete/' . $gallery->id) }}">
                                                    <span class="label bg-red">
                                                         {{ trans('home.delete') }}
                                                    </span>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <td align="center" colspan="5">
                                            {{ trans('home.no_galleries') }}
                                        </td>
                                    @endif
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th class="sorting_asc" style="width: 20%;">
                                            Título
                                        </th>
                                        <th class="sorting hidden-xs" style="width: 15%;">
                                            Autor
                                        </th>
                                        <th class="sorting" style="width: 10%;">
                                            Acciones
                                        </th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        @if (count($galleries) > 0)
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="dataTables_info" id="example1_info" role="status" aria-live="polite">
                                        {{ getPaginationText($galleries, trans('home.galleries_min')) }}
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="dataTables_paginate paging_simple_numbers pull-right">
                                        {!! $galleries !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>
@endsection

@section('custom-javascript')
    {!! Html::script('admin/assets/js/galleries.js') !!}
@endsection
