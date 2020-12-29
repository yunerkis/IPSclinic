import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AdminComponent } from './admin/admin.component';
import { AppointmentComponent } from './appointment/appointment.component';
import { DoctorScheduleComponent } from './doctor-schedule/doctor-schedule.component';
import { HomeAdminComponent } from './home-admin/home-admin.component';
import { ListComponent } from './list/list.component';
import { LoginComponent } from './login/login.component';
import { RecoveryComponent } from './recovery/recovery.component';
import { ScheduleComponent } from './schedule/schedule.component';

const routes: Routes = [
  { path: '', component: LoginComponent },
  { path: 'appointment', component: AppointmentComponent },
  { path: 'list', component: ListComponent },
  { path: 'admin', component: AdminComponent },
  { path: 'recovery', component: RecoveryComponent },
  { path: 'home', component: HomeAdminComponent },
  { path: 'schedule', component: ScheduleComponent },
  { path: 'doctor-schedule', component: DoctorScheduleComponent },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
