import { Component, OnInit, ViewChild } from '@angular/core';
import { ClientService } from '../services/client.service';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute } from "@angular/router";
import { MatCalendar } from '@angular/material/datepicker';

@Component({
  selector: 'app-doctor-schedule',
  templateUrl: './doctor-schedule.component.html',
  styleUrls: ['./doctor-schedule.component.scss']
})
export class DoctorScheduleComponent implements OnInit {

  @ViewChild('calendar', {static: false}) calendar: MatCalendar<Date>;
  
  doctorForm: FormGroup;
  dates: any = [];
  dayWeeks: any = [];
  daysSelected: any[] = [];
  event: any;
  doctor: any = null;
  id: any = null;
 
  constructor(
    private clientService: ClientService,
    private formBuilder: FormBuilder,
    private route: ActivatedRoute,
  ) { }

  ngOnInit(): void {

     this.id = this.route.snapshot.paramMap.get("id");

    this.doctorForm = this.formBuilder.group({
      first_names: ['', Validators.required],
      last_names: ['', Validators.required],
      category_id: ['1', Validators.required],
      first_time_start: [''],
      first_time_end: [''],
      first_interval: [''],
      second_time_start: [''],
      second_time_end: [''],
      second_interval: [''],
    });    

    if (this.id) {
      this.clientService.getDoctor(this.id).subscribe( res => {
        this.doctor = res['data'][0];
        this.doctorForm = this.formBuilder.group({
          first_names: [this.doctor.first_names, Validators.required],
          last_names: [this.doctor.last_names, Validators.required],
          category_id: ['1', Validators.required],
          first_time_start: [ this.doctor.schedules[0] ? this.doctor.schedules[0].time_start: ''],
          first_time_end: [ this.doctor.schedules[0] ? this.doctor.schedules[0].time_end: ''],
          first_interval: [ this.doctor.schedules[0] ? this.doctor.schedules[0].time: ''],
          second_time_start: [ this.doctor.schedules[1] ? this.doctor.schedules[1].time_start: ''],
          second_time_end: [ this.doctor.schedules[1] ? this.doctor.schedules[1].time_end: ''],
          second_interval: [ this.doctor.schedules[1] ? this.doctor.schedules[1].time: ''],
        });   

        if (this.doctor.schedules[0]) {

          this.daysSelected =  this.doctor.schedules[0].dates
        } else if (this.doctor.schedules[1]) {
          
          this.daysSelected =  this.doctor.schedules[1].dates
        }
        
        this.calendar.updateTodaysDate();
      });
    }
  }

  logout() {
    this.clientService.logout();
  }

  isSelected = (event: any) => {
    const date =
      event.getFullYear() +
      "-" +
      ("00" + (event.getMonth() + 1)).slice(-2) +
      "-" +
      ("00" + event.getDate()).slice(-2);

      return this.daysSelected.find(x => x == date) ? "selected" : null;
  };

  onSelect(event: any, calendar: any) {
    const date =
      event.getFullYear() +
      "-" +
      ("00" + (event.getMonth() + 1)).slice(-2) +
      "-" +
      ("00" + event.getDate()).slice(-2);
    const index = this.daysSelected.findIndex(x => x == date);
    if (index < 0) this.daysSelected.push(date);
    else this.daysSelected.splice(index, 1);

    calendar.updateTodaysDate();
  }

  onSubmit() {
    if(this.doctorForm.invalid || 
      (this.doctorForm.value.first_time_start == '' && this.doctorForm.value.second_time_start == '') || 
      (this.doctorForm.value.first_time_end == '' && this.doctorForm.value.second_time_end == '') ||
      (this.doctorForm.value.first_interval == '' && this.doctorForm.value.second_interval == '') ||
      this.daysSelected.length == 0) {
        return;
    }
    
    this.doctorForm.value.dates = [];

    if (this.doctorForm.value.first_time_start != '') {
      this.doctorForm.value.dates.push({
        "dayWeeks": "[]",
        "dates": this.daysSelected.toString(),
        "time_start": this.doctorForm.value.first_time_start,
        "time_end": this.doctorForm.value.first_time_end,
        "time": this.doctorForm.value.first_interval,
        "id": this.doctor ? (this.doctor.schedules[0] ? this.doctor.schedules[0].id : null) : null
      });
    }

    if (this.doctorForm.value.second_time_start != '') {
      this.doctorForm.value.dates.push({
        "dayWeeks": "[]",
        "dates": this.daysSelected.toString(),
        "time_start": this.doctorForm.value.second_time_start,
        "time_end": this.doctorForm.value.second_time_end,
        "time": this.doctorForm.value.second_interval,
      "id": this.doctor ? (this.doctor.schedules[1] ? this.doctor.schedules[1].id : null) : null
      });
    }

    if (this.id) {
      this.clientService.updateDoctor(this.id, this.doctorForm.value);
    } else {
      this.clientService.storeDoctor(this.doctorForm.value);
    }
    
  }
}
