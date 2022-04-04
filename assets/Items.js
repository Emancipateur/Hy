import React ,{ useState} from 'react';
import Calendar from 'react-calendar';
import 'react-calendar/dist/Calendar.css';
import { differenceInCalendarDays, differenceInDays} from "date-fns";
import './styles/calendar.scss'


function Item({suite, dateArray}) {

  // Date selectioné par l'utilisateur
  const [date, setDate] = useState(new Date());
  const [debutUser, setDebutUser] = useState(new Date());
  const [finUser, setFinUser] = useState(new Date());




//Recupère Client Id from symfony
const [client, setClient] = useState('')
  document.addEventListener('DOMContentLoaded', function() {
    var jsClient = document.querySelector('.client-js');
    var clientId = jsClient.dataset.isClient;

  setClient(clientId)
  });

  //Recupère Prix suite from symfony
  const [suitePrix, setSuitePrix] = useState(0)
  document.addEventListener('DOMContentLoaded', function() {
    var jsSuitePrix = document.querySelector('.prix-js');
    var suitePrix = jsSuitePrix.dataset.isPrix;

  setSuitePrix(suitePrix)
  });



// fonction de React-Calendar permettant de desactiver certain jours
const tileDisabled = ({ date, view }) => {

  if (view === 'month') {
    // Renvoi à React-Calendar les jours égales a ceux déjà reservés.
    return dateArray.find(dDate => differenceInCalendarDays(dDate, date) === 0);
  
}}

const selectedDate = (e) => {
  setDate(e)
  setDebutUser(e[0])
  setFinUser(e[1])

}

 const sendData = (e) => {
e.preventDefault();

  // fetch('https://emancipateur.com/hypnos/public/api/reservations', {
  fetch('https://127.0.0.1:8000/api/reservations', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({debut: debutUser, fin: finUser, clients: '/api/clients/'+client, suites : "/api/suites/"+ suite})
      
   }).then((res) => {
      if(res.ok)
        window.location.href='http://127.0.0.1:8000/reservation/sucess'
        else{
          alert('La réservation ne sais pas déroulé correctement, veuillez recommencer')
   }
  })}




  
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
    onChange={(e) => selectedDate(e)}
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
      <p>Reservation du {debutUser.toLocaleDateString()} au {finUser.toLocaleDateString()} </p>
      <p>Soit un Total de {differenceInDays(finUser,debutUser)} Nuit(s)</p>
      <p>Total : {differenceInDays(finUser,debutUser) * suitePrix}  </p>
    
    </div>
    </div>
    </>
    );


    


}
export default Item;