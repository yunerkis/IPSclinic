import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-home-admin',
  templateUrl: './home-admin.component.html',
  styleUrls: ['./home-admin.component.scss']
})
export class HomeAdminComponent implements OnInit {

  constructor() { }

  onFileComplete(data: any) {
    console.log(data); // We just print out data bubbled up from event emitter.
  }

  ngOnInit(): void {
  }

}
