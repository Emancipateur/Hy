import React ,{ useState, useEffect, useCallback} from 'react';
import Calendar from 'react-calendar';
import 'react-calendar/dist/Calendar.css';
import { differenceInCalendarDays, differenceInDays} from "date-fns";
import './styles/calendar.scss'


function Item() {
  const [client, setClient] = useState('')
  const [suite, setSuite] = useState('')
 const [debut, setDebut] = useState([]);
 const [fin, setFin] = useState([]);
const [booked,setBooked] = useState([]);
const [suitePrix, setSuitePrix] = useState(0)

//Recupère Client Id from symfony
  document.addEventListener('DOMContentLoaded', function() {
    var jsClient = document.querySelector('.client-js');
    var clientId = jsClient.dataset.isClient;

  setClient(clientId)
  });
  document.addEventListener('DOMContentLoaded', function() {
    var jsSuitePrix = document.querySelector('.prix-js');
    var suitePrix = jsSuitePrix.dataset.isPrix;

  setSuitePrix(suitePrix)
  });

//Recupère Suite Id from symfony
  document.addEventListener('DOMContentLoaded', function() {
    var jsSuite = document.querySelector('.suite-js');
    var suiteId = jsSuite.dataset.isSuite;
    setSuite(suiteId)
  });
  
  
  useEffect(() => {
    fetch("https://localhost:8000/api/reservations", {
      method : 'GET',
      mode : 'cors',
    headers: {
      'Accept': 'application/ld+json'
    }})
      .then((res) => res.json())
      .then((data) => setBooked(data["hydra:member"]));
    
  }, []);

// Tri les réservations par rapport a la suite
useEffect(()=> {
 const book = booked.filter(book => book.suites.includes(suite))
  setDebut(book.map(b => b.debut))
  setFin(book.map(b => b.fin))
  
  }, [booked]);


  
// useEffect(()=> {
// setDebut(booked.map(o => (o.debut)))
// setFin(booked.map(o => (o.fin)))

// }, [booked]);

function isSameDay(a, b) {
  return differenceInCalendarDays(a, b) === 0;
}

  Date.prototype.addDays = function(days) {
    var date = new Date(this.valueOf());
    date.setDate(date.getDate() + days);
    return date;
  }

  var dateArray = new Array();
function getDates(startDate, stopDate) {

  var currentDate = startDate.addDays(1);
  while (currentDate <= stopDate) {
      dateArray.push(new Date (currentDate));
      currentDate = currentDate.addDays(1);
  }
  return dateArray;
} 


for (let i = 0; i <= dateArray.length; i ++ ) {
var dateArray =  getDates(new Date(debut[i]), (new Date(fin[i])))

}



const tileDisabled = ({ date, view }) => {

  // Add class to tiles in month view only
  if (view === 'month') {
    // Check if a date React-Calendar wants to check is within any of the ranges
    return dateArray.find(dDate => isSameDay(dDate, date));
  

}}


const a = (e) => {
  setDate(e)
  setD(e[0])
  setF(e[1])

}



 const sendData = (e) => {
// e.preventDefault();


  // fetch('https://emancipateur.com/hypnos/public/api/reservations', {
  fetch('https://127.0.0.1:8000/api/reservations', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({debut: d, fin: f, clients: '/api/clients/'+client, suites : "/api/suites/"+ suite})
      
    }).then(res => window.location.href='http://127.0.0.1:8000/reservation/sucess')
   }

  const [date, setDate] = useState(new Date());
  const [d, setD] = useState(new Date());
  const [f, setF] = useState(new Date());


  
  const login = (e) => {
    e.preventDefault()
    // window.location.href='https://emancipateur.com/hypnos/public/login';
    window.location.href='https://127.0.0.1:8000/login';
  }

  return (
    <>
    <div className="calendarContent">
    <h1>Faire une Reservation</h1>
    <form className='calendarForm'>
    <Calendar
    onChange={(e) => a(e)}
    value={date}
    minDate={new Date()}
    tileDisabled={tileDisabled}
    selectRange={true}
    />
    { client !== '' ? (
  <button className='reservationButton' onClick={sendData}>Reserver</button>
    ) : <button className='reservationButton' onClick={(e) => login(e)}>Reserver ( Connexion )</button>
}
    </form>
    <div className='reservationNewDetails'>
      <p>Reservation du {d.toLocaleDateString()} au {f.toLocaleDateString()} </p>
      <p>Soit un Total de {differenceInDays(f,d)} Nuit(s)</p>
      <p>Total : {differenceInDays(f,d) * suitePrix}  </p>
    
    </div>
    </div>
    </>
    );


    


}
export default Item;