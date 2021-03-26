<style>
    .centrat {
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
<div class="centrat">
    <h1>Contacte desde la web</h1>

    <ul>
        <li><em>Email: </em> {{$data->email}}</li>
        <li><em>Nom: </em> {{$data->name}}</li>
        <li><em>Assumpte: </em> {{$data->subject}}</li>
        <li><em>Missatge: </em> {{$data->message}}</li>
    </ul>

    <p>
        Enviat {{ $data->send_it }}
    </p>

</div>


