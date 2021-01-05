import { Component, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { ModalViewComponent } from '../modal-view/modal-view.component';
import Swal from 'sweetalert2';
import { ClientService } from '../services/client.service';

@Component({
  selector: 'app-schedule',
  templateUrl: './schedule.component.html',
  styleUrls: ['./schedule.component.scss']
})
export class ScheduleComponent implements OnInit {

  doctorsList: any = '';
  msglog: any;

  constructor(
    private clientService: ClientService,
    public dialog: MatDialog
  ) { }

  openModal() {
    const dialogRef = this.dialog.open(ModalViewComponent);

    dialogRef.afterClosed().subscribe(result => {
      console.log(`Dialog result: ${result}`);
    });
  }

  ngOnInit(): void {
    this.clientService.getDoctorList().subscribe(res => {
      this.doctorsList = res['data'];
    });
  }

  logout() {
    this.clientService.logout();
  }

  getSchedule(id) {

    this.clientService.getDoctor(id).subscribe(res => {
      if (res) {
        this.msglog = {
          'type': 'success',
          'doctor': res['data'][0].first_names+' '+res['data'][0].last_names,
          'schedule': res['data'][0]
        };
        this.clientService.viewModal.next(this.msglog)
        this.openModal();
      }
    });
  }

  delete(id) {
     Swal.fire({
      title: '¿Deseas eliminar este horario?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Eliminar'
    }).then((result) => {
      if (result.isConfirmed) {
        this.clientService.deleteDoctor(id).subscribe(res => {
          this.clientService.getDoctorList().subscribe(resp => {
            this.doctorsList = resp['data'];
            Swal.fire(
              'Eliminado',
              'El horario fue eliminado',
              'success'
            )
          });
        });
      }
    })
  }

}
