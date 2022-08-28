import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { ClientService } from '../services/client.service';
import { MatDialog } from '@angular/material/dialog';
import { ModalComponent } from '../modal/modal.component';
import { environment } from '../../environments/environment';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-upload-result',
  templateUrl: './upload-result.component.html',
  styleUrls: ['./upload-result.component.scss']
})
export class UploadResultComponent implements OnInit {

  searchForm: FormGroup;
  excelForm: FormGroup;
  msglog: any;
  userClientsList: any = [];
  results: any = [];
  url = environment.url;
  msg = '';
  id = '';
  dni = '';

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

    this.searchForm = this.formBuilder.group({
      search: [''],
    });

    this.excelForm = this.formBuilder.group({
      result: ['', Validators.required],
    });

    this.clientsList();
  }

  logout() {
    this.clientService.logout();
  }

  onFileSelect(event) {
    const file = event.target.files[0];
    this.excelForm.get('result').setValue(file);
  }

  onSubmit() {
    this.msg = '';
    if(this.excelForm.invalid) {
      return;
    }
    const formData = new FormData();
    formData.append('result', this.excelForm.get('result').value);
    this.clientService.uploadClientResult(formData, this.id).subscribe(res => {
      Swal.fire(
        'Carga completa',
        'Carga de resultado exitosa',
        'success'
      ).then(() => {
        this.excelForm.reset();
        this.clientResults(this.dni, this.id)
      });
      
    }, error => {
      Swal.fire(
        'Error',
        'Error al cargar resultado',
        'error'
      )
      console.log(error.error.data);
    })
  }

  onSearch() {

    this.clientsList(this.searchForm.value);
    this.results = [];
  }

  clientResults(dni, id) 
  {
    this.clientService.getClientResults(dni).subscribe(
      res => {
        if (res) {
          this.results = res['data'];
          this.id = id;
          this.dni = dni;
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


  clientsList(search?)
  {
    this.clientService.getClientList(search).subscribe(
      res => {
        if (res) {
          this.userClientsList = res['data'];
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

  deleteResult(id) 
  {
    this.clientService.destroyResult(id);
  }
}
