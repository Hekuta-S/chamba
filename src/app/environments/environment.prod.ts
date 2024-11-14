interface Environment {
    production: boolean;
    dominio: string;
    path: string;
    version: string;
}

export const environment : Environment = {
    production: true,
    dominio: './proxy/',
    path: '',
    version: '1.0.0',
};
