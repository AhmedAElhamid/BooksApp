<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h3>Books Added Report.</h3>
    <p>Hi, {{ $name }}</p>
    <p>Added {{ count($summaryReport->getBooksAdded()) }} books and {{ count($summaryReport->getBooksFailed()) }} failed</p>
    @if(count($summaryReport->getBooksAdded()) > 0)
        <h4>Added</h4>
        <ul>
            @foreach($summaryReport->getBooksAdded() as $book)
                <li>{{ $book }}</li>
            @endforeach
        </ul>
    @endif
    @if(count($summaryReport->getBooksFailed()) > 0)
        <h4>Failed</h4>
        <ul>
            @foreach($summaryReport->getBooksFailed() as $book)
                <li>{{ $book }}</li>
            @endforeach
        </ul>
    @endif
</body>
</html>

