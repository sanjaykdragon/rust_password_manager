use std::time::{SystemTime, UNIX_EPOCH, Duration};

pub fn get_epoch_time() -> u64 {
    let start = SystemTime::now();
    let since_the_epoch = start.duration_since(UNIX_EPOCH)
        .expect("Time went backwards (how?)");
    return since_the_epoch.as_secs();
}

pub fn get_input(prompt: &str) -> String{
    use std::io;
    println!("{}",prompt);
    let mut input = String::new();
    match io::stdin().read_line(&mut input) {
        Ok(_goes_into_input_above) => {},
        Err(_no_updates_is_fine) => {},
    }
    return input.trim_end().to_string();
}

pub fn get_readable_time(epoch_time: u64) -> String {
    use chrono::prelude::DateTime;
    use chrono::Utc;

    let d = UNIX_EPOCH + Duration::from_secs(epoch_time);
    let datetime = DateTime::<Utc>::from(d);
    let timestamp_str = datetime.format("%Y-%m-%d %H:%M").to_string();
    return timestamp_str;
}