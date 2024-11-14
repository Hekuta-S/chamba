/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{html,ts}",
  ],
  theme: {
    extend: {
      colors: {
        negros:{
          DEFAULT: '#000000'
        },
        blancos:{
          DEFAULT: '#FFFFFF',
          w100: '#FDFDFD',
          w200: '#FDFDFF'
        },
        grises:{
          g100: '#F4F4F4',
          g150: '#E6E6E6',
          g200: '#DBDBDB',
          g300: '#B9B9B9',
          g400: '#989898',
          g425: '#999999',
          g450: '#707070',
          g500: '#6C6C6C',
          g525: '#5E5E5E',
          g550: '#404040',
          g600: '#525252',
          g700: '#3C3C3C',
          g800: '#2D2D2D',
          g900: '#222222',
          overlay1: '#2F2F6033'
        },
        rojos:{
          r100: '#FCF3F3',
          r200: '#FFE3E3',
          r300: '#FFC5C1',
          r400: '#FC867F',
          r500: '#F65041',
          r600: '#DA291C',
          r625: '#D9291C',
          r650: '#E03224',
          r700: '#C7251A',
          r750: '#AC1523',
          r800: '#DA291C',
          r900: '#83170F'
        },
        azules:{
          az100: '#C7F9FC',
          az200: '#B2F3F6',
          az300: '#8CDDE1',
          az400: '#7CCCD0',
          az500: '#0097A9',
          az600: '#0093A3',
          az700: '#00818F',
          az800: '#006E7A',
          az900: '#005C66'
        },
        amarillos:{
          a100: '#FFFCE0',
          a200: '#FFF9AC',
          a300: '#FFF072',
          a400: '#FEE458',
          a500: '#F4D327',
          a600: '#FFC722',
          a700: '#FFAD17',
          a800: '#FA9C2D',
          a900: '#FF8300'
        },
        verdes:{
          v100: '#F3FFFC',
          v200: '#DDF9ED',
          v300: '#B2E5D3',
          v400: '#8AE2C3',
          v500: '#45CEA4',
          v600: '#04B58E',
          v650: '#00AE0C',
          v700: '#06A17B',
          v800: '#008C67',
          v900: '#006B4F'
        },
        estados:{
          exito: '#04B58E',
          error: '#B52217',
          alerta: '#FEE458',
          informacion: '#0097A9',
          stepper: '#006B4F'
        }
      },
      fontFamily: {
        amx: ['AMX', 'sans-serif'],
        roboto: ['Roboto', 'sans-serif'],
      },
      fontWeight: {
        normal: '400',
        medium: '500',
        bold: '700',
      },
      backgroundImage: {
        'header-gradient': 'linear-gradient(116deg, #E03224 0%, #AC1422 100%)',
      },
    },
  },
  plugins: [],
}

