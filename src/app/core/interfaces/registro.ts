export interface ICustomValidatorPassword {
    min:any,
    uppercase:any,
    cNumber:any,
    specialCharacter:any
}

export interface IRegistroForm {
    tipoDocumento: string;
    documento: string;
    correo: string;
    contrasena: string;
    confirmContrasena: string;
    terminos: boolean;
}