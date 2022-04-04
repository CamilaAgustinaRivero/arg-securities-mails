<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="{{route('documentacion-fisicas.store')}}" method="POST" enctype="multipart/form-data">
    @csrf    
    <label>
            <p>
                Nombre:
            </p>
            <input type="text" name="name">
        </label>
        
        <label>
            <p>
                Email:
            </p>
            <input type="email" name="email">
        </label>
        
        <label>
            <p>
                Mensaje:
            </p>
            <textarea name="mensaje"></textarea>
        </label>
        <label for="content">
            <p>
                Archivo:
            </p>
            <input type="file" name="file">
        </label>
        <label for="content2">
            <p>
                Archivo:
            </p>
            <input type="file" name="file2">
        </label>
        <div>
            <button type="submit">Enviar</button>
        </div>
    </form>

</body>
</html>