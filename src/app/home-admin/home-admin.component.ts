import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { ClientService } from '../services/client.service';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-home-admin',
  templateUrl: './home-admin.component.html',
  styleUrls: ['./home-admin.component.scss']
})
export class HomeAdminComponent implements OnInit {

  excelForm: FormGroup;
  msg = '';

  constructor(
    private formBuilder: FormBuilder,
    private clientService: ClientService,
  ) { }

  onFileComplete(data: any) {
    console.log(data); // We just print out data bubbled up from event emitter.
  }

  ngOnInit(): void {
    this.excelForm = this.formBuilder.group({
      file: ['', Validators.required],
    });
  }

  onFileSelect(event) {
    const file = event.target.files[0];
    this.excelForm.get('file').setValue(file);
  }

  logout() {
    this.clientService.logout();
  }
  

  onSubmit() {
    this.msg = '';
    if(this.excelForm.invalid) {
      return;
    }
    const formData = new FormData();
    formData.append('file', this.excelForm.get('file').value);
    this.clientService.uploadClientExcel(formData).subscribe(res => {
      Swal.fire(
        'Carga completa',
        'Carga de clientes exitosa',
        'success'
      )
      this.excelForm.reset();
    }, error => {
      Swal.fire(
        'Error',
        'Error al importar csv',
        'error'
      )
      console.log(error.error.data);
    })
  }
}
