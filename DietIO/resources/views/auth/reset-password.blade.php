<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            background-color: #FFE08365; /* Light orange background color */
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
        }
        h2 {
            color: #333; /* Darker text for better contrast */
        }
        .form-container {
            background-color: white; /* White background for the form */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: inline-block;
            width: 300px; /* Fixed width for consistency */
        }
        label {
            display: block; /* Make labels block elements */
            margin-top: 10px; /* Space between labels and inputs */
            text-align: left; /* Align labels to the left */
        }
        input[type="email"],
        input[type="password"] {
            padding: 10px;
            margin: 10px 0;
            width: 100%; /* Full width for inputs */
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            padding: 10px;
            background-color: #4CAF50; /* Green background for the button */
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-top: 15px; /* Space above the button */
        }
        input[type="submit"]:hover {
            background-color: #45a049; /* Darker green on hover */
        }
    </style>
</head>
<body>
<h2>IoDiet</h2>
<div class="form-container">
    <form action="/api/users/reset" method="post">
        <input type="email" id="email" name="email" value="{{$email}}" required placeholder="Email"><br>
        <input name="token" type="hidden" value="{{$token}}">
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="password" required placeholder="New Password"><br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="password_confirmation" required placeholder="Confirm Password"><br>
        <input type="submit" value="Reset Password">
    </form>
</div>
</body>
</html>
