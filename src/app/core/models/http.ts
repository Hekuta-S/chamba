export class ReqListarCuentasModel {
    DocumentNumber:string
    DocumentType:string
    DocumentTypeText:string
    esSolicitarRegistro:number
    LastName:string
    Name:string
    nombreUsuario:string
    password:string
    typeService:string

    constructor(documento:string, tipoDocumento:string){
        this.DocumentNumber = documento || '',
        this.DocumentType = tipoDocumento || '',
        this.DocumentTypeText = "",
        this.esSolicitarRegistro = 0,
        this.LastName = "",
        this.Name = "",
        this.nombreUsuario = "",
        this.password = "",
        this.typeService = ""
    }
}

export class ReqVerifyIdClaro {
    documentClient:string
    modulo:string
    constructor ({ documentClient = '', modulo = '' }) {
      this.documentClient = documentClient
      this.modulo = modulo
    }
}