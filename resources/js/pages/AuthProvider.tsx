import {
    createContext,
    PropsWithChildren,
    useContext,
    useEffect,
    useLayoutEffect,
    useState,
} from 'react';
import axios from 'axios';

const AuthContext = createContext(undefined);

export const useAuth = () => {
  const authContext = useContext(AuthContext);
  if (!authContext) throw new Error('useAuth must be used within AuthProvider')
  return authContext;
};

export const AuthProvider = ({children}: PropsWithChildren) => {
    const [token, setToken] = useState('');

    useEffect(() => {
        const fetchMe = async () => {
            try {
                const response = await axios.get('/api/me')
                setToken(response.data.access_token)
            } catch {
                setToken('');
            }
        }
        fetchMe().then(r => undefined);
    }, []);

    useLayoutEffect(() => {
        const authInterceptor = axios.interceptors.request.use((config) => {
            // @ts-ignore _retry
            config.headers.Authorization = !config._retry && token ? `Bearer ${token}` : config.headers.Authorization;
            return config;
        });
        return () => {
            axios.interceptors.request.eject(authInterceptor);
        }
    }, [token]);

    useLayoutEffect(() => {
        const refreshInterceptor = axios.interceptors.response.use((response) => response, async (error) => {
            const originalRequest = error.config;
            if (error.response.status === 403 && error.response.data.message === 'Unauthorized') {
                try {
                    const response = await axios.get('/api/auth/refresh');
                    setToken(response.data.access_token)
                    originalRequest.headers.Authorization = `Bearer ${token}`;
                    originalRequest._retry = true;
                    return axios(originalRequest);
                } catch {
                    setToken('');
                }
            }
            return Promise.reject(error);
        });
        return () => {
            axios.interceptors.request.eject(refreshInterceptor);
        }
    }, []);

    return (
        <AuthContext.Provider value={undefined}>
          {children}
        </AuthContext.Provider>
    );
};
