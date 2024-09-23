import './bootstrap';
import Alpine from 'alpinejs';
import angular from 'angular';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Alpine = Alpine;
Alpine.start();

// Initialize AngularJS app
angular.module('socialApp', [])

// Configure Pusher and Laravel Echo
.run(function() {
    window.Pusher = Pusher;

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: 'd006c53948c8e840c514',
        cluster: 'ap1',
        forceTLS: true,
        encrypted: true,
    });
})

// PostController
.controller('PostController', function($scope, $http) {
    $scope.posts = [];
    $scope.notifications = [];
    $scope.newPost = {};
    $scope.isDropdownOpen = false;

    // Toggle dropdown visibility
    $scope.toggleDropdown = function() {
        $scope.isDropdownOpen = !$scope.isDropdownOpen;
    };

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.querySelector('.relative');
        if (!dropdown.contains(event.target)) {
            $scope.$apply(() => {
                $scope.isDropdownOpen = false;
            });
        }
    });

    // Function to calculate relative time
    $scope.timeAgo = function(date) {
        const now = new Date();
        const seconds = Math.floor((now - new Date(date)) / 1000);
        let interval = Math.floor(seconds / 31536000);
        if (interval > 1) return interval + " years ago";
        interval = Math.floor(seconds / 2592000);
        if (interval > 1) return interval + " months ago";
        interval = Math.floor(seconds / 86400);
        if (interval > 1) return interval + " days ago";
        interval = Math.floor(seconds / 3600);
        if (interval > 1) return interval + " hours ago";
        interval = Math.floor(seconds / 60);
        if (interval > 1) return interval + " minutes ago";
        return seconds < 2 ? "Just now" : seconds + " seconds ago";
    };

    // Fetch posts
    $scope.getPosts = function() {
        $http.get('/posts')
            .then(function(response) {
                $scope.posts = response.data;
            }, function(error) {
                console.error('Error fetching posts:', error);
            });
    };

    // Fetch notifications
    $scope.getNotifications = function() {
        $http.get('/notifications')
            .then(function(response) {
                $scope.notifications = response.data;
            }, function(error) {
                console.error('Error fetching notifications:', error);
            });
    };

    // Like a post
    $scope.likePost = function(post) {
        $http.post(`/posts/${post.id}/like`)
            .then(function(response) {
                if (response.data.message === 'Post liked') {
                    post.likes_count++;
                    post.userHasLiked = true;
                } else if (response.data.message === 'Post unliked') {
                    post.likes_count--;
                    post.userHasLiked = false;
                }
            }, function(error) {
                console.error('Error toggling like:', error);
            });
    };

    // Add a comment
    $scope.addComment = function(post) {
        $http.post(`/posts/${post.id}/comment`, { comment: post.newComment })
            .then(function(response) {
                post.comments.push(response.data);
                post.newComment = ''; // Clear the comment input
            }, function(error) {
                console.error('Error adding comment:', error);
            });
    };

    // Delete a post
    $scope.deletePost = function(post) {
        if (confirm('Are you sure you want to delete this post?')) {
            $http.delete(`/posts/${post.id}`)
            .then(function(response) {
                const index = $scope.posts.indexOf(post);
                if (index > -1) {
                    $scope.posts.splice(index, 1);
                }
                alert(response.data.message);
            }, function(error) {
                console.error('Error deleting post:', error);
            });
        }
    };

    // Mark notification as read
    $scope.markAsRead = function(notificationId) {
        $http.post(`/notifications/${notificationId}/read`)
            .then(function(response) {
                $scope.notifications = $scope.notifications.map(notification => {
                    if (notification.id === notificationId) {
                        notification.is_read = true;
                    }
                    return notification;
                });
            }, function(error) {
                console.error('Error marking notification as read:', error);
            });
    };

    // Delete notification
    $scope.deleteNotification = function(notificationId) {
        if (confirm('Are you sure you want to delete this notification?')) {
            $http.delete(`/notifications/${notificationId}`)
                .then(function(response) {
                    $scope.notifications = $scope.notifications.filter(n => n.id !== notificationId);
                }, function(error) {
                    console.error('Error deleting notification:', error);
                });
        }
    };

    // Initialize the controller
    $scope.init = function() {
        $scope.getPosts();
        $scope.getNotifications();
    };

    $scope.init();
});
