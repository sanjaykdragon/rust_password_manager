use serde::Deserialize;
use serde_json::json;
use std::fmt;

use crate::utils;

#[derive(Deserialize, Debug)]
pub struct CredentialList {
    pub values: Vec<Credential>,
}

#[derive(Deserialize, Debug, Default)]
pub struct Credential {
    pub username: String,
    pub password: String,
    pub site: String,
    pub time: String
}

impl fmt::Display for Credential {
    fn fmt(&self, f: &mut fmt::Formatter<'_>) -> fmt::Result {
        let time_num: u64 = self.time.parse().expect("self.time is invalid, needed u64");
        let readable_time = utils::get_readable_time(time_num);
        write!(f, "[{}] user: {} | pass: {} | site: {}", readable_time, self.username, self.password, self.site)
    }
}

pub struct WebStorage {
    pub base_url: String
}

impl WebStorage {
    pub fn send_credentials(&self, creds: Credential) {
        let path_to_post = self.base_url.clone() + "test.php";
        let resp = ureq::post(
            path_to_post.as_str()
        ).send_json(
            json!({
                "username" : creds.username,
                "password" : creds.password,
                "site" : creds.site,
                "option" : "save" //save credentials to db
            })
        );

        
        if resp.ok() {
            let response_str = resp.into_string().expect("unable to turn request into str");
            println!("{}", response_str);
        }
        else {
            println!("server returned error code {}", resp.status())
        }
    }

    fn get_credentials_list(&self) -> String {
        let path_to_post = self.base_url.clone() + "test.php";
        let resp = ureq::post(
            path_to_post.as_str()
        ).send_json(
            json!({
                "option" : "get_list" //get list of credentials from db
            })
        );

        if resp.ok() {
            let return_val = resp.into_string().expect("unable to unwrap string, server returned something weird?");
            return return_val;
        }
        else {
            println!("server returned error code {}", resp.status());
            let return_val = "{\"status\": \"error\"}";
            return String::from(return_val);
        }
    }

    pub fn get_creds(&self) -> Option<CredentialList> {
        let response_string = self.get_credentials_list();
        let response_as_str = response_string.as_str();
        let response: serde_json::Value = serde_json::from_str(response_as_str).expect("failed to convert to json");
        if response["status"] == "success" {
            let return_val: CredentialList = serde_json::from_str(response_as_str).expect("failed to convert to json");
            return Some(return_val);
        }
        else {
            return None;
        }
    }
}