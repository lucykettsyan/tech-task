
import axios from 'axios';

export const performSearch = async (params) => await axios.get('/search', {
    params: params
  }
);
