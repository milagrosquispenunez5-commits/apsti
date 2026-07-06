//========================
// MENÚ ACTIVO
//========================

const sections = document.querySelectorAll("section");
const navLinks = document.querySelectorAll(".sidebar ul li a");

window.addEventListener("scroll", () => {

    let current = "";

    sections.forEach(section => {

        const sectionTop = section.offsetTop - 150;

        if (pageYOffset >= sectionTop) {

            current = section.getAttribute("id");

        }

    });

    navLinks.forEach(link => {

        link.classList.remove("active");

        if(link.getAttribute("href") == "#" + current){

            link.classList.add("active");

        }

    });

});


//========================
// ANIMACIÓN AL HACER SCROLL
//========================

const elementos = document.querySelectorAll(
".card, .espacio, .semestre, .docente, .titulo, .texto, .imagen");

function mostrarElementos(){

    elementos.forEach(el=>{

        const posicion = el.getBoundingClientRect().top;

        const pantalla = window.innerHeight;

        if(posicion < pantalla-120){

            el.classList.add("mostrar");

        }

    });

}

window.addEventListener("scroll",mostrarElementos);

mostrarElementos();


//========================
// BOTÓN VOLVER ARRIBA
//========================

const boton = document.createElement("button");

boton.innerHTML="⬆";

boton.id="subir";

document.body.appendChild(boton);

window.addEventListener("scroll",()=>{

    if(window.scrollY>500){

        boton.style.display="block";

    }else{

        boton.style.display="none";

    }

});

boton.onclick=()=>{

    window.scrollTo({

        top:0,

        behavior:"smooth"

    });

};


//========================
// EFECTO DE ESCRITURA
//========================

const titulo = document.querySelector(".hero h1");

const texto = titulo.innerHTML;

titulo.innerHTML="";

let i=0;

function escribir(){

    if(i<texto.length){

        titulo.innerHTML += texto.charAt(i);

        i++;

        setTimeout(escribir,40);

    }

}

escribir();


//========================
// EFECTO EN BOTONES
//========================

const botones=document.querySelectorAll("a, button");

botones.forEach(btn=>{

    btn.addEventListener("mouseenter",()=>{

        btn.style.transform="scale(1.05)";

    });

    btn.addEventListener("mouseleave",()=>{

        btn.style.transform="scale(1)";

    });

});