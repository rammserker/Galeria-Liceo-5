console.log('Arrancando la sandunga v5...');

const evtSource = new EventSource("api/eventos.php");

evtSource.addEventListener('ping', evt => {
    const time = JSON.parse(evt.data).time;

    console.log(`PING en ${time}`);
});

evtSource.addEventListener('cococh', evt => {
    console.log(`Hay un cococh: ${evt.data}`);
})