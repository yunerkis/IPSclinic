import { Component, OnInit } from '@angular/core';
import { ClientService } from '../services/client.service';

@Component({
  selector: 'app-list',
  templateUrl: './list.component.html',
  styleUrls: ['./list.component.scss']
})
export class ListComponent implements OnInit {

  sessionsList: any = '';

  constructor(
    private clientService: ClientService,
  ) { }

  ngOnInit() {
    this.clientService.getListSessions().subscribe(res => {
      this.sessionsList = res['data'];
    });
  }

  logout() {
    this.clientService.logout();
  }

  cancel(id) {

    this.clientService.cancelSession(id).subscribe(res => {
      this.clientService.getListSessions().subscribe(resp => {
        this.sessionsList = resp['data'];
      });
    });
  }

}
