import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { AdminComponent } from './admin/admin.component';
import { LoginComponent } from './login/login.component';
import { ListComponent } from './list/list.component';
import { AppointmentComponent } from './appointment/appointment.component';
import { FormDoctorComponent } from './form-doctor/form-doctor.component';
import { UploadResultComponent } from './upload-result/upload-result.component';
import { MaterialModule } from './material.module';

import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import {HttpClientModule} from '@angular/common/http';
import { ModalComponent } from './modal/modal.component';
import { RecoveryComponent } from './recovery/recovery.component';
import { HomeAdminComponent } from './home-admin/home-admin.component';
import { ScheduleComponent } from './schedule/schedule.component';
import { ModalViewComponent } from './modal-view/modal-view.component';
import { DoctorScheduleComponent } from './doctor-schedule/doctor-schedule.component';
import { WebPageComponent } from './web-page/web-page.component';
import { AboutUsComponent } from './web-page/about-us/about-us.component';
import { ServiceComponent } from './web-page/service/service.component';
import { DutiesComponent } from './web-page/duties/duties.component';

@NgModule({
  declarations: [
    AppComponent,
    AdminComponent,
    LoginComponent,
    ListComponent,
    AppointmentComponent,
    ModalComponent,
    RecoveryComponent,
    HomeAdminComponent,
    ScheduleComponent,
    ModalViewComponent,
    DoctorScheduleComponent,
    WebPageComponent,
    AboutUsComponent,
    ServiceComponent,
    DutiesComponent,
    FormDoctorComponent,
    UploadResultComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    BrowserAnimationsModule,
    MaterialModule,
    FormsModule,
    ReactiveFormsModule,
    HttpClientModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
