import React ,{ useState, useEffect} from 'react';
import Item from './Items';



export const CheckReservation = () => {

// Stock les réservations
const [debut, setDebut] = useState([]);
const [fin, setFin] = useState([]);
const [reserver, setReserver] = useState([]);

//Recupère Suite Id from symfony
const [suite, setSuite] = useState('')

document.addEventListener('DOMContentLoaded', function() {
    var jsSuite = document.querySelector('.suite-js');
    var suiteId = jsSuite.dataset.isSuite;
    setSuite(suiteId)
  });
  

// Récupère les réservations en base de donnée
useEffect(() => {
    fetch("https://localhost:8000/api/reservations", {
      method : 'GET',
      mode : 'cors',
    headers: {
      'Accept': 'application/ld+json'
    }})
      .then((res) => res.json())
      .then((data) => setReserver(data["hydra:member"]));
    
  }, []);



// Tri les réservations par rapport a la suite
useEffect(()=> {
    const reservation = reserver.filter(book => book.suites.includes(suite))
     setDebut(reservation.map(resa => resa.debut))
     setFin(reservation.map(resa => resa.fin))
     
     }, [reserver]);


// fonction qui permet d'ajouter des jours a une date
     Date.prototype.addDays = function(days) {
        const date = new Date(this.valueOf());
        date.setDate(date.getDate() + days);
        return date;
      }
    
      const dateArray = new Array();
    
      // Retourne un tableau avec les dates déjà reservé pour la suite
    function getDates(startDate, stopDate) {
    
      let currentDate = startDate.addDays(1);
      while (currentDate <= stopDate) {
          dateArray.push(new Date (currentDate));
          currentDate = currentDate.addDays(1);
      }
    
      return dateArray;
    } 

    
    // Appel a la fonction et boucle sur les différentes réservations
    for (let i = 0; i <= dateArray.length; i ++ ) {
    const dateArray =  getDates(new Date(debut[i]), (new Date(fin[i]))) 
    }



  // DateArray contient toute les jours réservés de la suite
  return (
    <Item  suite={suite} dateArray={dateArray}/>
  )
}
