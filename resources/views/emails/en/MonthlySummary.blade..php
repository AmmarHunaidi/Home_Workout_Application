<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Accept CV</title>
    <style>
        body {
            font-family: sans-serif;
        }
    </style>
</head>

<body>
    <h1>Monthly Summary</h1>
    <hr>
    Hey <span
        style="
        font-size: large;
        font-weight: 600;
        color: darkorange;
    ">{{ $name }}</span>!
    <p>You have burnt {{$calories}} this month.
    </p>
    <p>You excersised {{$workout_count}} this month.</p>
    <h4></h4>
    <h4>Thank you for being part of our Application,</h4>
    <h4>The Vigor team.</h4>
</body>

</html>
