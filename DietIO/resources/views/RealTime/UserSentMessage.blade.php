<head>
    <title>User Sent Message</title>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>

        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('d313329efc4ce3ef837b', {
            cluster: 'ap2'
        });

        var channel = pusher.subscribe('DoctorSentMessage-channel');
        channel.bind('UserSentMessage', function(data) {
            console.log("Event received:", data); // Log the received data to the console

            // Display the event data in a user-friendly way
            var messageContainer = document.getElementById('messages');
            var newMessage = document.createElement('div');
            newMessage.textContent = data.message; // Set the message text
            messageContainer.appendChild(newMessage); // Append the new message to the container
        });
    </script>

</head>
<body>
<h1>Pusher Test</h1>
<!-- Container to display messages -->
<div id="messages" style="margin-top: 20px;">
    <h3>Notifications:</h3>
</div>
<p>
    Try publishing an event to channel <code>my-channel</code>
    with event name <code>my-event</code>.
</p>
</body>
