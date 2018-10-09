import { Component, OnInit } from '@angular/core';
import { DataService } from '../data.service';
import { Category, Difficulty, Question, ClientAnswer} from '../data.interface';

@Component({
  selector: 'app-quizz',
  templateUrl: './quizz.component.html',
  styleUrls: ['./quizz.component.css']
})
export class QuizzComponent implements OnInit {
  categories: Category[] = [];
  difficulties: Difficulty[] = [];
  questions: Question[] = [];

  selectCategory: number = 0;
  selectDifficulty: number = 0;
  nbQuestions: number = 10;

  isQcmReceived: boolean = false; // qcm reçu ou pas
  indexQuestion: number = 0;

  // tableau des réponses client
  client_answers: ClientAnswer[] = [];

  btnValidDisabled: boolean = true;
  isQcmCompleted: boolean = false;

  score: number = 0;

  constructor(private dataService: DataService) { }

  ngOnInit() {

    this.dataService.getCategories()
      .subscribe((res: Category[]) => {
        this.categories = res;
      });

    this.dataService.getDifficulties()
      .subscribe((res: Difficulty[]) => {
        this.difficulties = res;
      });
  }

  submit() {
    let params: string =
      `?cat=${this.selectCategory}&dif=${this.selectDifficulty}&nbq=${this.nbQuestions}`;

    this.dataService.getQuizz(params)
      .subscribe((res: Question[]) => {
        this.isQcmReceived = true;
        this.questions = res;
      })
  }

  validQuestion() {

    // passage à la question suivante
    if (this.indexQuestion < this.questions.length - 1) {
      this.indexQuestion++;
    } else {
      // fin du formulaire (dernière question)
      // envoi des réponses au serveur pour validation
      this.sendAnswers();
    }
    this.btnValidDisabled = true; // btn désactivé
  }

  makeChoice(choice, choice_list, choice_list_item) {
    // retrait de la classe selected sur l'élément
    // précédemment sélectionné (nettoyage)
    let choices = choice_list.children; // enfants du ul => tableau de li

    for (let i:number = 0; i<choices.length; i++) {
      choices[i].classList.remove('selected');
    }

    // mise en forme de l'élément (choix) sélectionné
    choice_list_item.classList.add('selected');

    let qid = this.questions[this.indexQuestion].id;
    let aid = choice.id;
    let answer: ClientAnswer = {qid: qid, aid: aid};
    //console.log('Question: ' + qid + ' => Choix:' + aid);

    // à chaque clic sur une réponse => deux possiblités
    // 1) l'id de la question en cours existe déjà dans le tableau
    // et donc une réponse a déjà fournie, du coup: on la remplace
    // 2) l'id de la question n'existe pas, du coup: on l'ajoute

    if (this.client_answers.length == 0) {
      //console.log('tableau vide, première insertion')
      this.client_answers.push(answer);
    }

    let qidx: number = this.isQuestionTreated(answer.qid);

    if (qidx == -1) {
      //console.log('Question non traitée, pas de réponse associée');
      // Ajout de la question/réponse dans le tableau des
      //réponses client
      this.client_answers.push(answer);
    } else {
      //console.log('Question déjà traitée, mise à jour de la réponse associée')
      this.client_answers[qidx].aid = answer.aid;
    }

    //console.log(this.client_answers);
    // bouton de validation devient actif
    this.btnValidDisabled = false;
  }


  // helpers
  // méthode déterminant si une question a déjà reçu une réponse
  isQuestionTreated(qid: number): number {
    for (let i:number=0; i<this.client_answers.length; i++) {
      // si trouvé, retourne la position (l'indice) de
      // la question dans le tableau
      if (this.client_answers[i].qid == qid) return i;
    }
    return -1; // si l'id de la question n'a pas été trouvé
    // on retourne -1
  }

  sendAnswers() {
    // envoi des réponses au serveur via dataService
    this.dataService.postClientAnswers(this.client_answers)
      .subscribe((res: any) => {
        this.score = res.result;
        this.isQcmCompleted = true;
      })
  }

  reset() {
    this.questions = [];
    this.selectCategory = 0;
    this.selectDifficulty = 0;
    this.nbQuestions = 10;
    this.isQcmReceived = false;
    this.indexQuestion = 0;
    this.client_answers = [];
    this.btnValidDisabled = true;
    this.isQcmCompleted = false;
  }

}


//
