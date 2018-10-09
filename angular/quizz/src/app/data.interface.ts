export interface Category {
  id: number;
  label: string
}

export interface Difficulty {
  id: number;
  label: string;
}

export interface Answer {
  id: number;
  label: string;
}

export interface Question {
  id: number;
  label: string;
  choices: Answer[];
}

export interface ClientAnswer {
  qid: number;
  aid: number; // une seule réponse possible
}
