import {FormEvent, useRef} from 'react';

const apiUrl = 'http://127.0.0.1:8000';

export default () => {
    const refHtmlForm = useRef<HTMLFormElement>(null);

    const onClick = async (e: FormEvent) => {
        e.preventDefault();
        if (!refHtmlForm.current) return;
        const authResponse = await fetch('/api/auth/token', {method: 'post'});
        const authResponseData: {access_token: string; expire_datetime: string} = await authResponse.json();
        //
        /*
        const response = await fetch(apiUrl + '/upload-directory', {
            body: new FormData(refHtmlForm.current),
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${authResponseData.access_token}`,
            },
            method: 'post',
        });
        const responseData = await response.json();
         */
        console.log(123, authResponseData);
    }
    return (
        <form
            action="http://127.0.0.1:8000/upload-directory"
            className="[&_*]:border [&_*]:p-4"
            encType="multipart/form-data"
            method="post"
            ref={refHtmlForm}
        >
            <input type="file" name="files" multiple/>
            <input onClick={onClick} type="submit" value="Upload Directory"/>
            <fieldset>
                <input type="radio" id="dw" name="dw" value="dw" />
                <label htmlFor="dw">DW</label>
                <input type="radio" id="btg" name="btg" value="btg" />
                <label htmlFor="btg">BTG</label>
                <input type="radio" id="itau" name="itau" value="itau" />
                <label htmlFor="itau">Itaú</label>
            </fieldset>
            <a href="/logout">Logout</a>
        </form>
    )
}
