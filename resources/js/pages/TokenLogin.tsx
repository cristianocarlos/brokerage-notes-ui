import {FormEvent, MouseEventHandler, useRef} from 'react';

export default () => {
    const refHtmlForm = useRef<HTMLFormElement>(null);

    const onClick = async (e: FormEvent) => {
        e.preventDefault();
        if (!refHtmlForm.current) return;
        const response = await fetch('/api/jwt-login', {
            body: new FormData(refHtmlForm.current),
            method: 'post',
        });
        const responseData = await response.json();
        localStorage.setItem('token', responseData.access_token);
        console.log(responseData, responseData.access_token);
    }

    const onAuthClick: MouseEventHandler<HTMLAnchorElement> = async (e) => {
        e.preventDefault();
        const response = await fetch(e.currentTarget.getAttribute('href') || '', {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
            },
        });
        const responseData = await response.json();
        console.log('onAuthClick', responseData);
    }

    const onOpenClick: MouseEventHandler<HTMLAnchorElement> = async (e) => {
        e.preventDefault();
        const response = await fetch(e.currentTarget.getAttribute('href') || '', {
            headers: {
                'Accept': 'application/json',
            },
        });
        const responseData = await response.json();
        console.log('onOpenClick', responseData);
    }

    return (
        <form
            className="[&_*]:border [&_*]:p-2"
            method="post"
            ref={refHtmlForm}
        >
            <input type="email" name="email" onChange={() => null} value="test@example.com"/>
            <input type="password" name="password" onChange={() => null} value="password"/>
            <button onClick={onClick} type="submit">Submit</button>
            <div>
                <a href="api/jwt-protected-data" onClick={onAuthClick}>Protegido com token</a>
                <a href="api/jwt-protected-data" onClick={onOpenClick}>Protegido sem token</a>
                <a href="api/jwt-user-data" onClick={onAuthClick}>Aberto com token</a>
                <a href="api/jwt-user-data" onClick={onOpenClick}>Aberto sem token</a>
            </div>
        </form>
    )
}
