import React ,{ useState, useEffect, useCallback} from 'react';
import Calendar from 'react-calendar';
import 'react-calendar/dist/Calendar.css';
import { differenceInCalendarDays } from 'date-fns';
import './styles/calendar.scss'


function Item() {
  const [client, setClient] = useState('')
  const [suite, setSuite] = useState('')
 const [debut, setDebut] = useState([]);
 const [fin, setFin] = useState([]);
const [booked,setBooked] = useState([]);

//Recupère Client Id from symfony
  document.addEventListener('DOMContentLoaded', function() {
    var jsClient = document.querySelector('.client-js');
    var clientId = jsClient.dataset.isClient;

  setClient(clientId)
  });

//Recupère Suite Id from symfony
  document.addEventListener('DOMContentLoaded', function() {
    var jsSuite = document.querySelector('.suite-js');
    var suiteId = jsSuite.dataset.isSuite;
    setSuite(suiteId)
  });
  
  
  useEffect(() => {
    fetch("https://localhost:8000/api/reservations", {
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

  var currentDate = startDate;
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


  fetch('https://localhost:8000/api/reservations', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({debut: d, fin: f, clients: '/api/clients/'+client, suites : "/api/suites/"+ suite})
      
    });
  

   }

  const [date, setDate] = useState(new Date());
  const [d, setD] = useState(new Date());
  const [f, setF] = useState(new Date());
  const route = "{{ path('blog_show', {slug: 'my-blog-post'})|escape('js') }}"
  const login = (e) => {
    e.preventDefault()
    window.location.href='http://127.0.0.1:8000/login';
  }

  return (
    <form>
    <Calendar
    onChange={(e) => a(e)}
    value={date}
    tileDisabled={tileDisabled}
    selectRange={true}
    />
    { client !== '' ? (
  <button onClick={sendData}>Reserver</button>
    ) : <button onClick={(e) => login(e)}>Se connecter</button>
}
    </form>
    );


    


}
export default Item;