export default () => {
    return (
        <form action="http://127.0.0.1:8000/upload">
            <input className="border" type="file" />
            <button type="submit">Enviar</button>
        </form>
    )
}
