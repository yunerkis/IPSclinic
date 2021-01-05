import { Component, OnInit } from '@angular/core';
import { ClientService } from '../services/client.service';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { stringify } from '@angular/compiler/src/util';
import { ActivatedRoute } from "@angular/router";

@Component({
  selector: 'app-doctor-schedule',
  templateUrl: './doctor-schedule.component.html',
  styleUrls: ['./doctor-schedule.component.scss']
})
export class DoctorScheduleComponent implements OnInit {

  doctorForm: FormGroup;
  dates: any = [];
  dayWeeks: any = [];
  daysSelected: any[] = [];
  event: any;
  doctor: any = null;
  arrayDay1:any = null;
  arrayDay2:any = null;

  constructor(
    private clientService: ClientService,
    private formBuilder: FormBuilder,
    private route: ActivatedRoute,
  ) { }

  ngOnInit(): void {

    const id = this.route.snapshot.paramMap.get("id");

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

    if (id) {
      this.clientService.getDoctor(id).subscribe( res => {
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

         this.arrayDay1 =  this.doctor.schedules[0].dates ? this.doctor.schedules[0].dates : null;

         this.arrayDay2 =  this.doctor.schedules[1].dates ? this.doctor.schedules[1].dates : null;
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
        "time": this.doctorForm.value.first_interval
      });
    }

    if (this.doctorForm.value.second_time_start != '') {
      this.doctorForm.value.dates.push({
        "dayWeeks": "[]",
        "dates": this.daysSelected.toString(),
        "time_start": this.doctorForm.value.second_time_start,
        "time_end": this.doctorForm.value.second_time_end,
        "time": this.doctorForm.value.second_interval
      });
    }

    this.clientService.storeDoctor(this.doctorForm.value);
  }
}
