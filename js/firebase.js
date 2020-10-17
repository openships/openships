//firebase設定
let firebaseConfig = {
  apiKey: "AIzaSyDHm3RWKapUQ88yj8EknwQCs6HexAYtviU",
  authDomain: "openships-d4ed0.firebaseapp.com",
  databaseURL: "https://openships-d4ed0.firebaseio.com",
  projectId: "openships-d4ed0",
  storageBucket: "openships-d4ed0.appspot.com",
  messagingSenderId: "844104410630",
  appId: "1:844104410630:web:4d5db330b3582edbc64587",
  measurementId: "G-FKQBXZTC1S"
};

//firebase初期化
firebase.initializeApp(firebaseConfig);
firebase.analytics();
const db=firebase.firestore();