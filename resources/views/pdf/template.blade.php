<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Create PDF</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>

<div class="container">

    <div class="row">
        <div class="col-md-12">
        <h1>user list</h1>

        <table class="table table-hover table-bordered">
        <thead>
            <tr>
                <th>id</th>
                <th>usename</th>
                <th>E-mail</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
            </tr>
            @endforeach
        </tbody>
        </table>
        </div>
    </div> 
</div>
</body>
</html>