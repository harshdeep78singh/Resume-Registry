<html>
    <head>
        <title>Harshdeep Singh</title>
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="HandheldFriendly" content="true">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
        <style>
            .a{
                max-width:500px;
                margin:auto;
                margin-top:12%;
            }
        </style>
    </head>
    <body>
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="index.php" style="margin-left:1%">Resume Registry</a>
      <div class="collapse navbar-collapse"> </div>
      <a class="navbar-brand" href="login.php">Sign In</a>
      </nav>
      <div class='container-fluid'>
        <div class='a'> 
            <pre><h2>Sign Up</h2></pre>
            <form method="POST" action="login.php">
                <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Username</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="who" id="inputEmail" placeholder="Email">
                </div>
                </div>
                <br>
                <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="Email" class="form-control" name="mail" id="inputEmail" placeholder="Email">
                </div>
                </div>
                <br>
                <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" name="pass" id="inputPassword" placeholder="Password">
                </div>
                </div>
                <button type="submit" name="login" class="btn btn-primary btn-lg btn-block" style="margin-top:18px;width:6em">Sign Up</button>
                <button type="button" class="btn btn-primary btn-lg btn-block" style="margin-left:10px;margin-top:18px;width:5em" onclick="location.href='./index.php'">Cancel</button>
            </form>
        </div>
      </div>
    </body>
</html>