import { Component, OnInit } from '@angular/core';
import { ClientService } from '../services/client.service';
import { Router } from '@angular/router';
import { MatDialog } from '@angular/material/dialog';
import { ModalComponent } from '../modal/modal.component';
import { environment } from '../../environments/environment';
declare var require: any;

@Component({
  selector: 'app-appointment',
  templateUrl: './appointment.component.html',
  styleUrls: ['./appointment.component.scss']
})
export class AppointmentComponent implements OnInit {

  Holidays = require('date-holidays');
  hd = new this.Holidays('CO');
  url = environment.url;
 

  client: any = [];
  results: any = [];
  date: any;
  dateStart: any;
  timeStart: any;
  currentDate = new Date();
  nextDate = new Date(new Date().setDate(this.currentDate.getDate() + 2));
  selectedDate: any;
  schedules: any = [];
  msglog: any;
  doctor: any;
  isSchedules = 'x';
  isDoctor = '';
  rut = '900219765-2'

  minDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), this.currentDate.getDate());
  maxDate = new Date(this.nextDate.getFullYear(), this.nextDate.getMonth(), this.nextDate.getDate());

  today = new Date();
  time = this.today.getHours() + ":" + this.today.getMinutes() + ":" + this.today.getSeconds();

  constructor(
    private router: Router,
    private clientService: ClientService,
    public dialog: MatDialog
  ) { }

  openDialog() {
    this.dialog.closeAll();
    const dialogRef = this.dialog.open(ModalComponent);

    dialogRef.afterClosed().subscribe(result => {
      console.log(`Dialog result: ${result}`);
    });
  }

  filterDates = (event: any) => {
    const date = event.getDay();
    const holiday = this.hd.isHoliday(new Date(event));
    if (date == 0 || holiday != false) {

      return false;
    }

    return true;
  };

  ngOnInit(): void {
    this.validationsDayRange(this.minDate, this.maxDate);

    this.onSelect(this.minDate);

    this.clientService.client.subscribe( res => {
      this.client = res;

      if (Object.keys(this.client).length  != 0) {

        if (res['session']) {

          this.dateStart = res['session'].date;
          this.timeStart = res['session'].time_start;
          this.doctor = res['session'].doctor.first_names+ ' '+ res['session'].doctor.last_names;
        }
 
        this.clientService.getClientResults(this.client.dni).subscribe(
          res => {
            if (res) {
              this.results = res['data'];
            }
          }, data => {
              this.msglog = {
                'type' : 'Error',
                'msg': 'Error, por favor volver a cargar la página.'
              };
              this.clientService.modal.next(this.msglog)
              this.openDialog();
              console.log(data);
          });
      } 
    });

    
    

    if (Object.keys(this.client).length  == 0) {
      this.router.navigate(['']);
    }
  }

  validationsDayRange(date1, date2) {
    let current = date1;

    let newDay = date2;

    this.minDate = new Date(current.getFullYear(), current.getMonth(), current.getDate());

    while(date1 <= date2) {

      let holiday = this.hd.isHoliday(new Date(date1));

      if (date1.getDay() == 0 || holiday != false) {

        newDay = new Date(newDay.setDate(newDay.getDate() + 1));

        this.maxDate = new Date(newDay.getFullYear(), newDay.getMonth(), newDay.getDate());

        date1 = new Date(date1.setDate(date1.getDate() + 1));
      } else {

        date1 = new Date(date1.setDate(date1.getDate() + 1));
      } 
    }
  }

  onSelect(event) {
    this.selectedDate = event;
    this.toggleSchedule('x' , 'x');
    this.date = new Date(event).getFullYear()+'-'+("0" + (new Date(event).getMonth()+1)).slice(-2)+'-'+("0" + (new Date(event).getDate())).slice(-2);
    this.clientService.getSessionsSchedule(this.date, this.time).subscribe(res => {
      this.schedules = res['data'];
    })
  }

  toggleSchedule(schedule , doctor) {
    
    if (this.isSchedules == schedule && this.isDoctor == doctor) {

      this.doctor = 'x';
      this.isSchedules = 'x';
    } else {

      this.isDoctor = doctor;
      this.isSchedules = schedule;
    }
  }

  session(schedule, doctorObj, hours) {
    
    let session = {
      'dni': this.client.dni,
      'date': this.date,
      'time': schedule,
      'doctor_id':doctorObj.id 
    };
    
    this.clientService.storeSession(session).subscribe( 
      res => {
        if (res) {
          this.msglog = {
            'type': 'success',
            'doctor': doctorObj.first_names+' '+doctorObj.last_names,
            'time': hours
          };
          this.clientService.modal.next(this.msglog)
          this.openDialog();
        }
      }, data => {
          this.msglog = {
            'type' : 'Error',
            'msg': 'Error, por favor volver a cargar la página.'
          };
          this.clientService.modal.next(this.msglog)
          this.openDialog();
          console.log(data);
      });
  }

}
