<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Shop Manager</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="{{asset('/css/schedules.css')}}">

</head>
<body>
<div class="container">
    <div class="errors alert alert-danger" role="alert">

    </div>
    <div class="filters">
        <div class="d-flex justify-content-start">
            <div class="input-group col-md-3">
                <select class="custom-select" name="rotaId" id="inputSelectRota">
                    <option value="">Choose Rota</option>
                    @if(isset($rotas))
                        @foreach($rotas as $rota)
                            <option value={{$rota->id}}>{{$rota->week_commence_date}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="input-group col-md-3">
                <select class="custom-select" name="shopId" id="inputSelectShop">
                    <option value="">Choose Shop</option>
                    @if(isset($shops))
                        @foreach($shops as $shop)
                            <option value="{{$shop->id}}">{{$shop->name}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="input-group-append">
                <button class="btn btn-outline-secondary submit-selections" type="submit">Enter</button>
            </div>
        </div>
    </div>

    <div class="staff-info disabled">
        <table class="table table-hover">
            <h5 class="rota-date"></h5>
            <thead>
            <tr>
                <th scope="col">Week day</th>
                <th scope="col">Staff bonus times</th>
            </tr>
            </thead>
            <tbody>


            </tbody>
        </table>
    </div>

</div>
<script
        src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
        crossorigin="anonymous"></script>
<script src={{asset('/js/schedules.js')}}></script>
</body>
</html>