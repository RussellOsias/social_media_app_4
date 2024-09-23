<x-app-layout>
    <x-slot name="header">
        <div class="bg-blue-600 p-4 rounded-t-lg shadow-md flex justify-between items-center">
            <h2 class="text-white text-2xl font-semibold">
                {{ __('Notifications') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow-md mt-4">
            @if ($notifications->isEmpty())
                <p class="text-gray-500">No notifications available.</p>
            @else
                <ul id="notification-list">
                    @foreach ($notifications as $notification)
                        <li class="border-b border-gray-200 py-2 notification-item" data-id="{{ $notification->id }}">
                            <p>
                                <span class="font-bold">{{ $notification->actor->name }}</span> 
                                {{ $notification->type }} your post 
                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                                @if ($notification->is_read)
                                    <span class="text-green-500 ml-2">(Seen)</span>
                                @endif
                            </p>
                            <button 
                                class="mark-as-read text-blue-500 hover:underline" 
                                @if (!$notification->is_read) 
                                    data-read="{{ $notification->id }}" 
                                    @endif
                            >
                                @if ($notification->is_read)
                                    Seen
                                @else
                                    Mark as Read
                                @endif
                            </button>
                            <button 
                                class="delete-notification text-red-500 hover:underline" 
                                data-id="{{ $notification->id }}"
                            >
                                Delete
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.mark-as-read').forEach(button => {
                button.addEventListener('click', function() {
                    const notificationId = this.getAttribute('data-read');
                    if (notificationId) {
                        fetch(`/notifications/${notificationId}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.message) {
                                // Update the button text and styling
                                this.textContent = 'Seen';
                                this.classList.add('text-green-500');
                                this.classList.remove('text-blue-500');
                                this.removeAttribute('data-read'); // Remove the data attribute
                            }
                        });
                    }
                });
            });

            document.querySelectorAll('.delete-notification').forEach(button => {
                button.addEventListener('click', function() {
                    const notificationId = this.getAttribute('data-id');
                    if (notificationId) {
                        fetch(`/notifications/${notificationId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.message) {
                                document.querySelector(`.notification-item[data-id='${notificationId}']`).remove();
                            }
                        });
                    }
                });
            });
        });
    </script>
</x-app-layout>
