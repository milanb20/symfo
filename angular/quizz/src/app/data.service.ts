import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { ClientAnswer } from './data.interface';


@Injectable({
  providedIn: 'root'
})
export class DataService {
  private api: string = 'http://localhost:8000/api';

  // Injection du service Http afin de réaliser des requêtes Ajax
  constructor(private http: HttpClient) { }

  getCategories() {
    // .get renvoie un objet de type Observable (Rxjs)
    // On ne souscrit pas au niveau du service
    return this.http.get(this.api + '/category');
  }

  getDifficulties() {
    return this.http.get(this.api + '/difficulty');
  }

  getQuizz(params: string) {
    return this.http.get(this.api + '/quizz' + params);
  }

  postClientAnswers(client_answers: ClientAnswer[]) {
    // transformation en JSON du tableau client_answers
    let json:string  = JSON.stringify({answers: client_answers});
    return this.http.post(this.api + '/quizz/answers', json);
  }

}




//
