import { AbstractControl, ValidatorFn, ValidationErrors } from '@angular/forms';

import { ICustomValidatorPassword } from '@core/interfaces/registro';

export const customPasswordValidator = (): ValidatorFn => {
    return (control: AbstractControl): ValidationErrors | null => {
        const valueInput = control.value;
    
        const capitalLetterRegex = /[A-Z]/;
        const specialCharacterRegex = /[!@#$%^&*(),.?":{}|<>]/;
        const numberRegex = /\d/;

        const validations:ICustomValidatorPassword = {
            min: "",
            uppercase: "",
            cNumber: "",
            specialCharacter: ""
        }

        if(valueInput == null){
            return validations;
        }

        validations.min = valueInput.length > 8;
        validations.uppercase = capitalLetterRegex.test(valueInput);
        validations.cNumber = numberRegex.test(valueInput);
        validations.specialCharacter = specialCharacterRegex.test(valueInput);

        for (const [key, value] of Object.entries(validations)) {
            if(!value){
                return validations;
            };
        }

        return null;
    };
};

export const confirmPasswordValidator = (): ValidatorFn =>{
    return (control: AbstractControl): {[key: string]: any} | null => {
        const contrasena = control.get('contrasena');
        const confirmContrasena = control.get('confirmContrasena');

        if(confirmContrasena?.value.length == 0){
          return null;
        }

        return contrasena?.value === confirmContrasena?.value
        ? null
        : { PasswordNoMatch: true };
    };
};

