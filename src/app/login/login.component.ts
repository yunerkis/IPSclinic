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

  constructor(
    private formBuilder: FormBuilder,
    private clientService: ClientService,
  ) { }

  ngOnInit(): void {
    this.dniForm = this.formBuilder.group({
      dni: ['', Validators.required],
    });
  }

  onSubmit() {
    if(this.dniForm.invalid) {
      return;
    }
    this.clientService.getSessionsClient(this.dniForm.value);
    this.clientService.msg.subscribe(
      res => {this.msg = res;}
    );
  }

}