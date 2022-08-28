import { Injectable } from '@angular/core';
import { CanActivate } from '@angular/router';
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class AuthService implements CanActivate {

  constructor(
    private router : Router,
  ) { }

  canActivate(): boolean {

    let token = localStorage.getItem('token');
  
    if (token) {

      return true;

    } else {
      
      this.router.navigate(['/admin']);
      
      return false;
    }
  }
}
