<x-app-layout>
    <!-- Header Section -->
    <x-slot name="header">
        <div class="bg-blue-600 p-4 rounded-t-lg shadow-md flex justify-between items-center">
            <h2 class="text-white text-2xl font-semibold">
                {{ __('Welcome, ' . Auth::user()->name) }}
            </h2>
            <div class="flex items-center">
                <a href="{{ route('notifications.index') }}" class="text-white hover:text-gray-300">
                    Notifications
                </a>
            </div>
        </div>
    </x-slot>

    <br>   <!-- Main Content Section -->
    <div class="flex max-w-7xl mx-auto sm:px-6 lg:px-8" ng-app="socialApp" ng-controller="PostController">
        
        <!-- Post Creation and Display Section -->
        <div class="w-full lg:w-2/3 pr-4">
            <!-- Create Post Form -->
            <form ng-submit="createPost()" class="bg-white p-6 rounded-lg shadow-md">
                <textarea ng-model="newPost.content" placeholder="What's on your mind?" required class="w-full rounded-lg p-2 border-gray-300"></textarea>
                <br>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg mt-2">
                    {{ __('Post') }}
                </button>
            </form>

            <!-- Display Posts -->
            <div ng-repeat="post in posts" class="bg-white p-6 rounded-lg shadow-md mt-4">
                <small>
                    <span class="text-xl font-bold">@{{ post.user.name }}</span> 
                    on <span>@{{ timeAgo(post.created_at) }}</span>
                </small>
                <br><br>
                <p class="text-xl font-semibold mb-4">@{{ post.content }}</p>
                <br>
                
                <!-- Like and Delete Buttons -->
                <button ng-click="likePost(post)" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    {{ __('Like') }} (@{{ post.likes_count }})
                </button>
                
                <!-- Delete Button (Only visible for the post author) -->
                <button ng-if="post.user_id === {{ Auth::id() }}" ng-click="deletePost(post)" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg">
                    {{ __('Delete') }}
                </button>
                <br><br>
                
                <!-- Add Comment Form -->
                <form ng-submit="addComment(post)">
                    <input type="text" ng-model="post.newComment" placeholder="Add a comment" class="w-full mt-2 p-2 border-gray-300 rounded-lg">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg mt-2">
                        {{ __('Comment') }}
                    </button>
                </form>

                <!-- Display Comments -->
                <ul class="mt-4">
                    <li ng-repeat="comment in post.comments" class="border-b border-gray-200 py-2">
                        <small>
                            <span class="text-xl font-bold">@{{ comment.user.name }}</span> 
                            on <span>@{{ timeAgo(comment.created_at) }}</span>
                        </small>
                        <br>
                        <p class="text-xl font-semibold mt-2">@{{ comment.comment }}</p>
                    </li>
                </ul>
            </div>
        </div>

        

    </div>

    <!-- CSRF Token for AngularJS -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</x-app-layout>