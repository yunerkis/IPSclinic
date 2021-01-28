import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { ClientService } from '../services/client.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {

  dniForm: FormGroup;
  msg = '';
  today = new Date();
  time = this.today.getHours() + ":" + this.today.getMinutes() + ":" + this.today.getSeconds();
  close = false;

  constructor(
    private formBuilder: FormBuilder,
    private clientService: ClientService,
  ) { }

  ngOnInit(): void {
    
    if (this.today.getHours() < 7 || this.today.getHours() >= 17) {
      this.close = true
    }
    
    this.dniForm = this.formBuilder.group({
      dni: ['', Validators.required],
    });
  }

  onSubmit() {
    if(this.dniForm.invalid) {
      return;
    }
    this.clientService.getSessionsClient(this.dniForm.value, this.time);
    this.clientService.msg.subscribe(
      res => {this.msg = res;}
    );
  }

}
