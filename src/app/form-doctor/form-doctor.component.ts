import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { ClientService } from '../services/client.service';
import { MatDialog } from '@angular/material/dialog';
import { ModalComponent } from '../modal/modal.component';

@Component({
  selector: 'app-form-doctor',
  templateUrl: './form-doctor.component.html',
  styleUrls: ['./form-doctor.component.scss']
})
export class FormDoctorComponent implements OnInit {

  loginForm: FormGroup;
  msglog: any;
  userList: any = [];

  constructor(
    private formBuilder: FormBuilder,
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

  ngOnInit(): void {
    this.loginForm = this.formBuilder.group({
      email: ['', Validators.required],
      password: ['', Validators.required],
    });

    this.userDoctorList();
  }

  logout() {
    this.clientService.logout();
  }

  onSubmit() {
    
    if(this.loginForm.invalid) {
      return;
    }

    this.clientService.storeUserDoctor(this.loginForm.value);
  }

  userDelete(id)
  {
    this.clientService.destroyUserDoctor(id);
  }

  userDoctorList()
  {
    this.clientService.listUserDoctor().subscribe(
      res => {
        if (res) {
          this.userList = res['data'];
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
