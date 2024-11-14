interface Environment {
    production: boolean;
    dominio: string;
    path: string;
    version: string;
}

export const environment : Environment = {
    production: false,
    dominio: '',
    path: '/api/',
    version: '1.0.0',
};
