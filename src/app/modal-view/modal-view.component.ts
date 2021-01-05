import { Component, OnInit } from '@angular/core';
import { ClientService } from '../services/client.service';

@Component({
  selector: 'app-modal-view',
  templateUrl: './modal-view.component.html',
  styleUrls: ['./modal-view.component.scss']
})
export class ModalViewComponent implements OnInit {

  data:any;

  constructor(
    private clientService: ClientService,
  ) { }

  ngOnInit(): void {
    this.clientService.viewModal.subscribe(res => {
      this.data = res;
    });
  }

}
