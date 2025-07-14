<h1>Hola, {{ $user->name }}</h1>
<p>Gracias por registrarte. Para activar tu cuenta haz clic en el siguiente enlace:</p>

<a href="{{ url('/activar-cuenta/' . $user->activation_token) }}">
    Activar mi cuenta
</a>

<p>Si no solicitaste esta activación, ignora este correo.</p>
