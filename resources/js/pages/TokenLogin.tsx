import {FormEvent, MouseEventHandler, useRef, useState} from 'react';

export default () => {
    const refHtmlForm = useRef<HTMLFormElement>(null);

    const [accessToken, setAccessToken] = useState('')

    const onClick = async (e: FormEvent) => {
        e.preventDefault();
        if (!refHtmlForm.current) return;
        const response = await fetch('/api/auth/login', {
            body: new FormData(refHtmlForm.current),
            method: 'post',
        });
        const responseData = await response.json();
        setAccessToken(responseData.access_token);
        console.log(responseData);
    }

    const onAuthClick: MouseEventHandler<HTMLAnchorElement> = async (e) => {
        e.preventDefault();
        const response = await fetch(e.currentTarget.getAttribute('href') || '', {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${accessToken}`,
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

    const onRefreshAuthClick: MouseEventHandler<HTMLAnchorElement> = async (e) => {
        e.preventDefault();
        const response = await fetch(e.currentTarget.getAttribute('href') || '', {
            method: 'post',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${accessToken}`,
            },
        });
        const responseData = await response.json();
        console.log('onRefreshAuthClick', responseData);
    }

    const onRefreshOpenClick: MouseEventHandler<HTMLAnchorElement> = async (e) => {
        e.preventDefault();
        const response = await fetch(e.currentTarget.getAttribute('href') || '', {
            method: 'post',
            headers: {
                'Accept': 'application/json',
            },
        });
        const responseData = await response.json();
        console.log('onRefreshOpenClick', responseData);
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
                <a href="api/protected-data" onClick={onAuthClick}>Protegido com token</a>
                <a href="api/protected-data" onClick={onOpenClick}>Protegido sem token</a>
            </div>
            <div>
                <a href="api/auth/refresh" onClick={onRefreshAuthClick}>Refresh com token</a>
                <a href="api/auth/refresh" onClick={onRefreshOpenClick}>Refresh sem token</a>
                <a href="api/auth/refresh-clear" onClick={onRefreshOpenClick}>Clearr</a>
                <a href="api/auth/logout" onClick={onRefreshAuthClick}>Logout</a>
            </div>
        </form>
    )
}
