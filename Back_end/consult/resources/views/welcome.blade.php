<!DOCTYPE html>

<head>
    <link rel="stylesheet" href="/app.css" />
</head>

<body>
    <ul>
        @foreach ($users as $user)
        <li>{{ $user->full_name_en }}</li>
        @endforeach
    </ul>
</body>