<div class="card card-horizontal no-padding z-depth-3-top">
    <div class="row align-items-center">
        <div class="col-md-6">
            <div class="card-body">
                <span class="d-block meta-category mb-4">
                    @foreach($article->categories as $category)
                        <a href="{{ route('post-category', ['slug' => $category->slug]) }}"
                           class="text-md strong-600">{{ $category->category }}</a>
                    @endforeach
                </span>
                <h3 class="heading heading-3 strong-500">
                    <a href="{{ route('article', ['slug' => $article->slug]) }}">{{ $article->title }}</a>
                </h3>
                <div class="excerpt">
                    <p class="mt-3 mb-0 text-lg line-height-1_8">{{ $article->description }}</p>
                </div>
            </div>

            <div class="card-footer border-0 pt-2 pb-4">
                <div class="row">
                    <div class="col">
                        <div class="block-author">
                            <div class="author-image author-image-xs">
                                <img src="{{ getGravatar($article->user->email) }}">
                            </div>
                            <div class="author-info">
                                <div class="author-name">
                                    <a href="{{ route('posts-user', ['username' => $article->user->username]) }}"
                                       class="text-md strong-600">{{ $article->user->name }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col text-right">
                        <ul class="inline-links inline-links--style-2">
                            <li></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-image-container card-image-right">
                <img src="{{ \App\Http\Controllers\ImageManagerController::getPublicImageUrl($article->image) }}"
                     class="card-image" width="600">
            </div>
        </div>
    </div>
</div>